package model;

import java.io.Serializable;

import java.lang.StringBuilder;

import javax.persistence.Id;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;

import javax.xml.bind.annotation.*;

import javax.persistence.*;

/**
 */

@Entity
@NamedQueries({
		@NamedQuery(name = "findAllQuestionss", query = "select myQuestions from Questions myQuestions"),
		@NamedQuery(name = "findQuestionsByAnswer", query = "select myQuestions from Questions myQuestions where myQuestions.answer = ?1"),
		@NamedQuery(name = "findQuestionsByAnswerContaining", query = "select myQuestions from Questions myQuestions where myQuestions.answer like ?1"),
		@NamedQuery(name = "findQuestionsByChoices", query = "select myQuestions from Questions myQuestions where myQuestions.choices = ?1"),
		@NamedQuery(name = "findQuestionsByChoicesContaining", query = "select myQuestions from Questions myQuestions where myQuestions.choices like ?1"),
		@NamedQuery(name = "findQuestionsByCode", query = "select myQuestions from Questions myQuestions where myQuestions.code = ?1"),
		@NamedQuery(name = "findQuestionsByCodeContaining", query = "select myQuestions from Questions myQuestions where myQuestions.code like ?1"),
		@NamedQuery(name = "findQuestionsById", query = "select myQuestions from Questions myQuestions where myQuestions.id = ?1"),
		@NamedQuery(name = "findQuestionsByPoints", query = "select myQuestions from Questions myQuestions where myQuestions.points = ?1"),
		@NamedQuery(name = "findQuestionsByPrimaryKey", query = "select myQuestions from Questions myQuestions where myQuestions.id = ?1"),
		@NamedQuery(name = "findQuestionsByRequired", query = "select myQuestions from Questions myQuestions where myQuestions.required = ?1"),
		@NamedQuery(name = "findQuestionsBySeriesId", query = "select myQuestions from Questions myQuestions where myQuestions.seriesId = ?1"),
		@NamedQuery(name = "findQuestionsByText", query = "select myQuestions from Questions myQuestions where myQuestions.text = ?1"),
		@NamedQuery(name = "findQuestionsByTextContaining", query = "select myQuestions from Questions myQuestions where myQuestions.text like ?1"),
		@NamedQuery(name = "findQuestionsByType", query = "select myQuestions from Questions myQuestions where myQuestions.type = ?1") })
@Table(catalog = "HACKATHON", name = "questions")
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(namespace = "Hackathon/com/model", name = "Questions")
@XmlRootElement
public class Questions implements Serializable {
	private static final long serialVersionUID = 1L;

	/**
	 */

	@Column(name = "id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@Id
	@XmlElement
	Integer id;
	/**
	 */

	@Column(name = "series_id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer seriesId;
	/**
	 */

	@Column(name = "text", length = 1024, nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String text;
	/**
	 */

	@Column(name = "type", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer type;
	/**
	 */

	@Column(name = "code", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String code;
	/**
	 */

	@Column(name = "points", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer points;
	/**
	 */

	@Column(name = "required", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Boolean required;
	/**
	 */

	@Column(name = "choices", length = 1024, nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String choices;
	/**
	 */

	@Column(name = "answer", length = 1024, nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String answer;

	/**
	 */
	public void setId(Integer id) {
		this.id = id;
	}

	/**
	 */
	public Integer getId() {
		return this.id;
	}

	/**
	 */
	public void setSeriesId(Integer seriesId) {
		this.seriesId = seriesId;
	}

	/**
	 */
	public Integer getSeriesId() {
		return this.seriesId;
	}

	/**
	 */
	public void setText(String text) {
		this.text = text;
	}

	/**
	 */
	public String getText() {
		return this.text;
	}

	/**
	 */
	public void setType(Integer type) {
		this.type = type;
	}

	/**
	 */
	public Integer getType() {
		return this.type;
	}

	/**
	 */
	public void setCode(String code) {
		this.code = code;
	}

	/**
	 */
	public String getCode() {
		return this.code;
	}

	/**
	 */
	public void setPoints(Integer points) {
		this.points = points;
	}

	/**
	 */
	public Integer getPoints() {
		return this.points;
	}

	/**
	 */
	public void setRequired(Boolean required) {
		this.required = required;
	}

	/**
	 */
	public Boolean getRequired() {
		return this.required;
	}

	/**
	 */
	public void setChoices(String choices) {
		this.choices = choices;
	}

	/**
	 */
	public String getChoices() {
		return this.choices;
	}

	/**
	 */
	public void setAnswer(String answer) {
		this.answer = answer;
	}

	/**
	 */
	public String getAnswer() {
		return this.answer;
	}

	/**
	 */
	public Questions() {
	}

	/**
	 * Copies the contents of the specified bean into this bean.
	 *
	 */
	public void copy(Questions that) {
		setId(that.getId());
		setSeriesId(that.getSeriesId());
		setText(that.getText());
		setType(that.getType());
		setCode(that.getCode());
		setPoints(that.getPoints());
		setRequired(that.getRequired());
		setChoices(that.getChoices());
		setAnswer(that.getAnswer());
	}

	/**
	 * Returns a textual representation of a bean.
	 *
	 */
	public String toString() {

		StringBuilder buffer = new StringBuilder();

		buffer.append("id=[").append(id).append("] ");
		buffer.append("seriesId=[").append(seriesId).append("] ");
		buffer.append("text=[").append(text).append("] ");
		buffer.append("type=[").append(type).append("] ");
		buffer.append("code=[").append(code).append("] ");
		buffer.append("points=[").append(points).append("] ");
		buffer.append("required=[").append(required).append("] ");
		buffer.append("choices=[").append(choices).append("] ");
		buffer.append("answer=[").append(answer).append("] ");

		return buffer.toString();
	}

	/**
	 */
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = (int) (prime * result + ((id == null) ? 0 : id.hashCode()));
		return result;
	}

	/**
	 */
	public boolean equals(Object obj) {
		if (obj == this)
			return true;
		if (!(obj instanceof Questions))
			return false;
		Questions equalCheck = (Questions) obj;
		if ((id == null && equalCheck.id != null) || (id != null && equalCheck.id == null))
			return false;
		if (id != null && !id.equals(equalCheck.id))
			return false;
		return true;
	}
}
